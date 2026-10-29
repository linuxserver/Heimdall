// Bundled libraries don't set their own globals, but app.js and the Blade
// views expect these on window.
import Sortable from "sortablejs";
import Huebee from "huebee";

window.Sortable = Sortable;
window.Huebee = Huebee;
