window.addEventListener('capture-panel-renders', async () => {

    console.log('rendering');

    let renders = document.querySelectorAll('[id^="panel-render-"]');

    let images = [];


    for (let render of renders) {

        const canvas = await html2canvas(render, {

            scale: 2,

            backgroundColor: '#ffffff',

            useCORS: true,

            allowTaint: false,

            logging: false,

        });


        images.push({
            id: render.id,
            image: canvas.toDataURL('image/png')
        });

    }


    Livewire.dispatch(
        'panel-images-created',
        {
            images: images
        }
    );

});
