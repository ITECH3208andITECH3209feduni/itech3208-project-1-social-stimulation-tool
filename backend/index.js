// MARK: - Standard Libraries
import express from "express";
import env from "#config/env.config.js";
import { StatusCodes } from "http-status-codes";

// MARK: - Packages Customization
import { loggerUtil, resUtil } from "#utils/index.js";
import mongoConfig from "#config/mongodb.config.js";

const app = express();

// MARK: - App Middlewares
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// MARK: - App API Routes
// TODO: - Register Routes Here!!!

// MARK: - Handle 404 Not Found
app.use(env.apiPrefix, (error, req, res, next) => {
    loggerUtil.error(`Handle general error: ${error.stack}`);
    resUtil.sendError(res, "Page Not Found", StatusCodes.NOT_FOUND);
});

// MARK: - Handle General Error
app.use((error, req, res, next) => {
    loggerUtil.error(`Handle general error: ${error.stack}`);
    resUtil.sendError(res, "Internal Server Error", StatusCodes.INTERNAL_SERVER_ERROR);
});

// MARK: - Main Application
const runApp = async () => {
    try {
        await mongoConfig.connect();
        app.listen(env.port, () => {
            loggerUtil.debug(`Server is run on port ${env.port}`);
        });
    } catch (error) {
        loggerUtil.error(`Application run failed: ${error}`);
    }
};

await runApp();
