// MARK: - Standard Libraries
import express from "express";
import { StatusCodes } from "http-status-codes";

// MARK: - Packages Customization
import { envConfig, apiConfig, mongoConfig } from "#config/index.js";
import { loggerUtil, resUtil } from "#utils/index.js";
import { CategoryRouter, LevelRouter, AuthRouter, UserRouter, VideoRouter, FeedbackRouter, WishlistRouter, ContactRouter } from "#routes/index.js";

const app = express();

// MARK: - App Middlewares
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// MARK: - App API Routes
app.use(apiConfig.category, CategoryRouter);
app.use(apiConfig.level, LevelRouter);
app.use(apiConfig.auth, AuthRouter);
app.use(apiConfig.user, UserRouter);
app.use(apiConfig.video, VideoRouter);
app.use(apiConfig.feedback, FeedbackRouter);
app.use(apiConfig.wishlist, WishlistRouter);
app.use(apiConfig.contact, ContactRouter);

// MARK: - Handle 404 Not Found
app.use((req, res, next) => {
    resUtil.sendError({
        res,
        message: "Page Not Found",
        statusCode: StatusCodes.NOT_FOUND,
    });
});

// MARK: - Handle General Error
app.use((error, req, res, next) => {
    loggerUtil.error(`[HandleGeneralError]: ${error.stack}`);
    resUtil.sendError({
        res,
        message: error.message || "Internal Server Error",
        statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
    });
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
