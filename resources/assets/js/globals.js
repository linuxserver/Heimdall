// The bundler wraps these UMD libraries as CommonJS, so they no longer set
// their own globals. app.js and the Blade views still expect them on window.
import Sortable from "sortablejs";
import Huebee from "huebee";

window.Sortable = Sortable;
window.Huebee = Huebee;
