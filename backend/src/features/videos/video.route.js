import express from "express";
import { bodyMw, authMw, fileMw } from "#middlewares/index.js";
import { multerConfig } from "#config/index.js";
import VideoController from "./video.controller.js";
import VideoSchema from "./video.validation.js";

const router = express.Router();

// POST /api/v1/videos — Create video + upload file (multipart/form-data)
// Fields: title, description, categoryId, levelId, tags[] + file: video
router.post(
    "/",
    authMw.authorizeRole([authMw.UserRole.user, authMw.UserRole.admin]),
    multerConfig.single("video"),
    fileMw.check,
    fileMw.validate(VideoSchema.uploadVideo),
    bodyMw.validate(VideoSchema.createVideo),
    VideoController.createVideo,
);

// PATCH /api/v1/videos/upload-video/:id — Upload video file to Cloudinary
router.patch(
    "/upload-video/:id",
    authMw.authorizeRole([authMw.UserRole.user, authMw.UserRole.admin]),
    multerConfig.single("video"),
    fileMw.check,
    fileMw.validate(VideoSchema.uploadVideo),
    VideoController.uploadVideo,
);

// PATCH /api/v1/videos/upload-thumbnail/:id — Upload thumbnail image to Cloudinary
router.patch(
    "/upload-thumbnail/:id",
    authMw.authorizeRole([authMw.UserRole.user, authMw.UserRole.admin]),
    multerConfig.single("thumbnail"),
    fileMw.check,
    fileMw.validate(VideoSchema.uploadThumbnail),
    VideoController.uploadThumbnail,
);

 router.patch(
    "/:id",
    authMw.authorizeRole([authMw.UserRole.user]),
    bodyMw.validate(VideoSchema.updateVideoInfo),
    VideoController.updateVideoInfo,
);

// GET /api/v1/videos — Get all published videos (public, with filters & pagination)
router.get("/", VideoController.getVideos);

// GET /api/v1/videos/:id — Get single video by ID (public)
router.get("/:id", VideoController.getVideoById);

// DELETE /api/v1/videos/delete/:id — Soft delete video (admin only)
router.delete(
    "/delete/:id",
    authMw.authorizeRole([authMw.UserRole.admin]),
    VideoController.deleteVideo,
);

export default router;
