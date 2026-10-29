import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig(({ mode }) => ({
  plugins: [
    laravel({
      input: ["resources/assets/sass/app.scss", "resources/assets/js/index.js"],
    }),
  ],
  build: {
    // `npm run dev` output is committed, so keep it readable
    minify: mode !== "development",
    rolldownOptions: {
      // CSS url()s to public/img are written relative to the built CSS in
      // public/build/assets. Vite leaves them as-is; this hides its warning.
      external: [/^\.\.\/\.\.\/img\//],
    },
  },
}));
