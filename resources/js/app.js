import './bootstrap';
import {HSCopyMarkup as FlowbiteInstances} from "preline";
import {Modal} from "flowbite";
import 'livewire-sortable'
import 'flowbite-datepicker';

import { initFlowbite } from 'flowbite';

Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
    succeed(({ snapshot, effect }) => {
        queueMicrotask(() => {
            initFlowbite();
        })
    })
})

document.addEventListener('livewire:navigated', () => {
    initFlowbite();
})
