import express from "express";
import { bodyMw, authMw, fileMw } from "#middlewares/index.js";
import { multerConfig } from "#config/index.js";
import VideoController from "./video.controller.js";
import VideoSchema from "./video.validation.js";

const publicRouter = express.Router();
const userRouter = express.Router();
const adminRouter = express.Router();

// MARK: - PUBLIC ROUTES
publicRouter.get("/", VideoController.getVideos);
publicRouter.get("/:id", VideoController.getVideoById);

// MARK: - USER ROUTES (Organization Management)
userRouter.post(
    "/",
    authMw.authorizeRole([authMw.UserRole.organization, authMw.UserRole.admin, authMw.UserRole.individual]),
    multerConfig.single("video"),
    fileMw.check,
    fileMw.validate(VideoSchema.uploadVideo),
    bodyMw.validate(VideoSchema.createVideo),
    VideoController.createVideo
);

userRouter.patch(
    "/upload-video/:id",
    authMw.authorizeRole([authMw.UserRole.organization, authMw.UserRole.admin]),
    multerConfig.single("video"),
    fileMw.check,
    fileMw.validate(VideoSchema.uploadVideo),
    VideoController.uploadVideo
);

userRouter.patch(
    "/upload-thumbnail/:id",
    authMw.authorizeRole([authMw.UserRole.organization, authMw.UserRole.admin]),
    multerConfig.single("thumbnail"),
    fileMw.check,
    fileMw.validate(VideoSchema.uploadThumbnail),
    VideoController.uploadThumbnail
);

userRouter.patch(
    "/:id",
    authMw.authorizeRole([authMw.UserRole.organization]),
    bodyMw.validate(VideoSchema.updateVideoInfo),
    VideoController.updateVideoInfo
);

// MARK: - ADMIN ROUTES
adminRouter.delete(
    "/:id",
    authMw.authorizeRole([authMw.UserRole.admin]),
    VideoController.deleteVideo
);

export { publicRouter as VideoPublicRouter, userRouter as VideoUserRouter, adminRouter as VideoAdminRouter };
