const path = require("path");
const chalk = require("chalk");
const fastify = require("fastify");
const config = require("./config");

/**
 * Bootstrap function
 */
async function main() {

  const server = fastify();
  const options = {
    path: path.join(__dirname, 'dist'),
    port: process.env.DIRECTUS_SERVER_PORT || 80,
    host: process.env.DIRECTUS_SERVER_HOST || "0.0.0.0"
  };

  server.register(require("fastify-static"), {
    root: options.path,
  });

  server.get("/status", function(_, res) {
    res.status(200).send({
      "status": "ok"
    });
  });

  server.get("/config.js", async(_, res) => {
    res.header("Content-Type", "text/javascript").send(await config.get());
  });

  server.listen(options.port, options.host, function(err, address) {
    if (err) {
      console.error(chalk.red(err));
      console.error(chalk.red(err.stack));
      process.exit(1);
    }
    console.log(chalk.green(`listening at ${address}`));
  });

  // Register termination signals for graceful shutdowns
  [ "SIGUSR2", "SIGINT", "SIGTERM", "SIGQUIT" ].forEach((signal) => {
    process.removeAllListeners(signal);
    process.on(signal, async () => {
      try {
        await server.close();
        process.exit(0);
      } catch(err) {
        console.error(chalk.red(err));
        console.error(chalk.red(err.stack));
        process.exit(1);
      }
    });
  });
}

/**
 * Initialize
 */
main().catch(function(err) {
  console.error(chalk.red(err));
  console.error(chalk.red(err.stack));
  process.exit(1);
});
