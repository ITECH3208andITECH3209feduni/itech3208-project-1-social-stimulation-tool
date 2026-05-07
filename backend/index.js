// MARK: - Standard Libraries
import express from "express";
import { StatusCodes } from "http-status-codes";

// MARK: - Packages Customization
import { envConfig, apiConfig, mongoConfig } from "#config/index.js";
import { loggerUtil, resUtil } from "#utils/index.js";
import {
    CategoryPublicRouter,
    CategoryAdminRouter,
    LevelPublicRouter,
    LevelAdminRouter,
    AuthRouter,
    UserRouter,
    VideoPublicRouter,
    VideoUserRouter,
    VideoAdminRouter,
    FeedbackRouter,
    WishlistRouter,
    ContactRouter
} from "#routes/index.js";

const app = express();

// MARK: - App Middlewares
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

// MARK: - App API Routes

// 1. PUBLIC ROUTES
app.use(apiConfig.auth, AuthRouter);
app.use(apiConfig.category, CategoryPublicRouter);
app.use(apiConfig.level, LevelPublicRouter);
app.use(apiConfig.video, VideoPublicRouter);
app.use(apiConfig.feedback, FeedbackRouter);

// 2. COMMON AUTHENTICATED ROUTES (Me/Profile)
app.use(apiConfig.me, UserRouter);
app.use(apiConfig.contact, ContactRouter);

// 3. ROLE-SPECIFIC ROUTES
// Individual
app.use(apiConfig.individual + "/wishlists", WishlistRouter);

// User Content Management (Individual & Organization)
app.use(apiConfig.me + "/videos", VideoUserRouter);

// 4. ADMIN ROUTES
app.use(apiConfig.manageCategory, CategoryAdminRouter);
app.use(apiConfig.admin + "/levels", LevelAdminRouter);
app.use(apiConfig.manageVideo, VideoAdminRouter);

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
