import Alpine from 'alpinejs';
import { dataGrid } from './grid';
import { registerPages } from './pages';

window.Alpine = Alpine;

Alpine.data('dataGrid', (config) => dataGrid(config));
registerPages(Alpine);

Alpine.start();
