// MARK: - Standard Libraries
import express from "express";
import { StatusCodes } from "http-status-codes";

// MARK: - Packages Customization
import { envConfig, apiConfig, mongoConfig } from "#config/index.js";
import { loggerUtil, resUtil } from "#utils/index.js";
import { CategoryRouter } from "#routes/index.js";

const app = express();

// MARK: - App Middlewares
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// MARK: - App API Routes
app.use(apiConfig.category, CategoryRouter);

// MARK: - Handle 404 Not Found
app.use(envConfig.apiPrefix, (error, req, res, next) => {
    loggerUtil.error(`Handle general error: ${error.stack}`);
    resUtil.sendError({ res, message: "Page Not Found", statusCode: StatusCodes.NOT_FOUND });
});

// MARK: - Handle General Error
app.use((error, req, res, next) => {
    loggerUtil.error(`Handle general error: ${error.stack}`);
    resUtil.sendError({ res, message: "Internal Server Error", statusCode: StatusCodes.NOT_FOUND });
});

// MARK: - Main Application
const runApp = async () => {
    try {
        await mongoConfig.connect();
        app.listen(envConfig.port, () => {
            loggerUtil.debug(`Server is run on port ${envConfig.port}`);
        });
    } catch (error) {
        loggerUtil.error(`Application run failed: ${error}`);
    }
};

await runApp();
