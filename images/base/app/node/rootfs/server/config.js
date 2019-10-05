module.exports = {
  /**
   * Gets the config data
   */
  data() {
    const prefix = "DIRECTUS_";
    const titleSuffix = "_TITLE";
    const urlSuffix = "_URL";

    const keys = Array.from(
      new Set(
        Object.keys(process.env)
          .map(key => key.toUpperCase())
          .filter(
            key =>
              key.startsWith(prefix) &&
              (key.endsWith(titleSuffix) || key.endsWith(urlSuffix))
          )
          .map(key => {
            const k = key.substr(prefix.length);
            if (k.endsWith(urlSuffix)) {
              return k.substr(0, k.length - urlSuffix.length);
            } else if (k.endsWith(titleSuffix)) {
              return k.substr(0, k.length - titleSuffix.length);
            }
            return k;
          })
      )
    );

    const entries = keys
      .filter(
        key =>
          `${prefix}${key}${titleSuffix}` in process.env &&
          `${prefix}${key}${urlSuffix}` in process.env
      )
      .map(key => ({
        [process.env[`${prefix}${key}${urlSuffix}`]]:
          process.env[`${prefix}${key}${titleSuffix}`]
      }))
      .reduce((prev, current) => Object.assign(prev, current));

    if (Object.values(entries).length == 0) {
      return "../_/";
    }

    const defaultValue = {
      api: entries,
      allowOtherAPI: "DIRECTUS_ALLOW_OTHER_API" in process.env || false,
      routerMode: process.env.DIRECTUS_ROUTER_MODE || "hash",
      routerBaseUrl: process.env.DIRECTUS_ROUTER_BASE_URL || "/",
      defaultLocale: process.env.DIRECTUS_DEFAULT_LOCALE || "en-US"
    };

    return defaultValue;
  },

  /**
   * Gets the final serving file
   */
  get() {
    const config = this.data();
    return `(function(){window.__DirectusConfig__=${JSON.stringify(
      config
    )}})();`;
  }
};
