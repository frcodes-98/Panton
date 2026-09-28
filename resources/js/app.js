import Alpine from 'alpinejs';
import { dataGrid } from './grid';
import { registerArea } from './area';
import { registerPages } from './pages';

window.Alpine = Alpine;

Alpine.data('dataGrid', (config) => dataGrid(config));
registerPages(Alpine);
registerArea(Alpine);

Alpine.start();
