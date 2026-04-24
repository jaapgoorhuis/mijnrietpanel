<?php

namespace App\Livewire\MarketingFolder\Marketing;

use App\Models\Marketing;
use App\Models\MarketingFolder;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exceptions\InvalidArgumentException;
use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\ImageManager;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Drivers\Gd\Driver;

class UploadMarketing extends Component
{
    use WithFileUploads;

    public $file;
    public $marketing;

    public $friendly_name = [];

    public $locale;
    public $folderId;

    public $folder;

    public $cropimage = []; // bestaande afbeeldingen per item
    public $newCropimage; // nieuwe afbeelding bij upload

    public function mount($id)
    {
        $this->folderId = $id;
        $this->folder = MarketingFolder::find($id);
    }

    public function render()
    {
        $this->locale = config('app.locale'); // leest APP_LOCALE uit .env

        if ($this->locale === 'nl') {
            $this->marketing = Marketing::orderBy('order_id', 'asc')
                ->where('lang', 'nl')
                ->where('marketing_category_id', $this->folderId)
                ->get();
        } elseif ($this->locale === 'en') {
            $this->marketing = Marketing::orderBy('order_id', 'asc')
                ->where('lang', 'en')
                ->where('marketing_category_id', $this->folderId)
                ->get();
        }

        foreach ($this->marketing as $item) {
            if (!isset($this->friendly_name[$item->id])) {
                $this->friendly_name[$item->id] = $item->friendly_name;
            }
        }

        return view('livewire.marketingFolder.marketing.uploadMarketing');
    }

    protected $rules = [


        'cropimage.*' => 'mimes:jpg,svg,jpeg,png,gif,webp|max:2048', // max 2MB
        'newCropimage' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp', // nieuwe afbeelding bij upload
    ];

    public function messages(): array
    {
        return [
            'cropimage.mimes' => 'Het bestand moet een afbeelding zijn',
            'newCropimage.image' => 'Het nieuwe bestand moet een afbeelding zijn',
            'newCropimage.max' => 'De afbeelding mag maximaal 2MB zijn',
            'newCropimage.required' => 'De thumbnail is verplicht',
        ];
    }

    public function updateOrder($orderList)
    {
        foreach ($orderList as $item) {
            Marketing::where('id', $item['value'])->update(['order_id' => $item['order']]);
        }
        $this->dispatch('updated');
    }

    /**
     * @throws InvalidArgumentException
     */
    public function uploadFiles()
    {
        $this->validate();

        if (!$this->file) {
            session()->flash('error', 'Upload één of meerdere bestanden');
            return;
        }

        $imageExtensions = ['jpg','jpeg','png','gif','webp','bmp'];

        $extension = strtolower($this->file->getClientOriginalExtension());

        $allowed = ['pdf','jpg','jpeg','png','gif','bmp','webp','svg','otf','dwg','rvt','rfa'];

        if (!in_array($extension, $allowed)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'files' => 'Bestandstype niet toegestaan.',
            ]);
        }

        $manager = new ImageManager(new Driver());

        // veilige bestandsnaam
        $fileName = uniqid() . '_' . pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $extension;
        $path = 'marketing/' . $fileName;

        // volgorde bepalen
        $latestMarketing = Marketing::orderBy('order_id', 'desc')->first();
        $orderId = $latestMarketing ? $latestMarketing->order_id + 1 : 1;

        // 🖼 IMAGE
        if (in_array($extension, $imageExtensions)) {

            $image = $manager->decodePath($this->file->getPathname());
            $image->scale(width: 1200);

            $encoded = $image->encode(new JpegEncoder(quality: 80));

            Storage::disk('public')->put($path, $encoded);

        } else {

            // 📄 NON-IMAGE (pdf, otf, dwg, rvt, rfa...)
            $this->file->storeAs('marketing', $fileName, 'public');
        }

        // database record
        $marketing = Marketing::create([
            'friendly_name' => $this->file->getClientOriginalName(),
            'file_name' => $fileName,
            'order_id' => $orderId,
            'marketing_category_id' => $this->folderId,
            'lang' => $this->locale,
        ]);

        // 🖼 CROPPED IMAGE
        if ($this->newCropimage) {

            $cropExtension = strtolower($this->newCropimage->getClientOriginalExtension());
            $cropName = uniqid() . '_crop.' . $cropExtension;
            $cropPath = 'marketing/' . $cropName;

            $cropImage = $manager->decodePath($this->newCropimage->getPathname());
            $cropImage->scale(width: 800);

            $encodedCrop = $cropImage->encode(new JpegEncoder(quality: 80));

            Storage::disk('public')->put($cropPath, $encodedCrop);

            $marketing->cropimage = $cropName;
            $marketing->save();
        }

        session()->flash('success', 'De bestanden zijn geupload');

        $this->reset(['file', 'newCropimage']);

        return $this->redirect(
            '/marketing-maps/' . $this->folderId . '/marketing/upload',
            navigate: true
        );
    }

    public function remove($id)
    {
        $marketing = Marketing::where('id', $id)->first();

        // verwijder bestand
        Storage::disk('public')->delete('marketing/' . $marketing->file_name);

        // verwijder cropimage indien aanwezig
        if ($marketing->cropimage) {
            Storage::disk('public')->delete($marketing->cropimage);
        }

        // delete record
        Marketing::where('id', $id)->delete();

        session()->flash('success', 'Het bestand is verwijderd');
    }

    public function updateItem($id)
    {
        $marketing = Marketing::find($id);

        // Naam updaten
        if(isset($this->friendly_name[$id])) {
            $marketing->friendly_name = $this->friendly_name[$id];
        }

        // Cropimage per item updaten
        if (isset($this->cropimage[$id])) {
            if($marketing->cropimage) {
                Storage::disk('public')->delete($marketing->cropimage);
            }

            $path = $this->cropimage[$id]->store('marketing', 'public');
            $marketing->cropimage = $path;

            // reset file input van dit item
            unset($this->cropimage[$id]);
        }

        $marketing->save();

        session()->flash('success', 'Item succesvol bijgewerkt');

        // reset het file input van dit item
        unset($this->cropimage[$id]);
    }
}
