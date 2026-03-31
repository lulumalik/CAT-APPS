// electron.vite.config.mjs
import { join } from "path";
import { defineConfig, externalizeDepsPlugin } from "electron-vite";
var electron_vite_config_default = defineConfig({
  main: {
    build: {
      rollupOptions: {
        plugins: [
          {
            name: "watch-external",
            buildStart() {
              this.addWatchFile(join(process.env.APP_PATH, "app", "Providers", "NativeAppServiceProvider.php"));
            }
          }
        ]
      }
    },
    plugins: [externalizeDepsPlugin()]
  }
});
export {
  electron_vite_config_default as default
};
