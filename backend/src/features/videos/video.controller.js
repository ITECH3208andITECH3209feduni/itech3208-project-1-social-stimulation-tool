import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";
import VideoService from "./video.service.js";
import VideoMessages from "./video.message.js";

const VideoController = {
    // POST /videos — Create video + upload file in one step
    createVideo: async (req, res) => {
        try {
            const userId = req.user._id || req.user.id;
            const payload = req.body;
            const file = req.file;

            const newVideo = await VideoService.createVideo({ payload, file, userId });

            return resUtil.sendSuccess({
                res,
                statusCode: StatusCodes.CREATED,
                message: VideoMessages.success.UPLOAD_VIDEO_SUCCESSFULLY,
                data: newVideo,
            });
        } catch (error) {
            loggerUtil.error(`[VideoController.createVideo]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    // PATCH /videos/:id/upload-video — Upload video file
    uploadVideo: async (req, res) => {
        try {
            const videoId = req.params.id;
            const file = req.file;

            const updatedVideo = await VideoService.uploadVideo({ videoId, file });

            return resUtil.sendSuccess({
                res,
                message: VideoMessages.success.UPLOAD_VIDEO_SUCCESSFULLY,
                data: updatedVideo,
            });
        } catch (error) {
            loggerUtil.error(`[VideoController.uploadVideo]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    // PATCH /videos/:id/upload-thumbnail — Upload thumbnail image
    uploadThumbnail: async (req, res) => {
        try {
            const videoId = req.params.id;
            const file = req.file;

            const updatedVideo = await VideoService.uploadThumbnail({ videoId, file });

            return resUtil.sendSuccess({
                res,
                message: VideoMessages.success.UPLOAD_THUMBNAIL_SUCCESSFULLY,
                data: updatedVideo,
            });
        } catch (error) {
            loggerUtil.error(`[VideoController.uploadThumbnail]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    // GET /videos — Get all published videos
    getVideos: async (req, res) => {
        try {
            const { categoryId, levelId, page, limit } = req.query;

            const result = await VideoService.getVideos({
                categoryId,
                levelId,
                page: parseInt(page) || 1,
                limit: parseInt(limit) || 10,
            });

            return resUtil.sendSuccess({
                res,
                message: VideoMessages.success.GET_VIDEOS_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[VideoController.getVideos]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    // GET /videos/:id — Get single video by ID
    getVideoById: async (req, res) => {
        try {
            const videoId = req.params.id;

            const video = await VideoService.getVideoById(videoId);

            return resUtil.sendSuccess({
                res,
                message: VideoMessages.success.GET_VIDEO_SUCCESSFULLY,
                data: video,
            });
        } catch (error) {
            loggerUtil.error(`[VideoController.getVideoById]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    // DELETE /videos/:id — Soft delete video
    deleteVideo: async (req, res) => {
        try {
            const videoId = req.params.id;

            const result = await VideoService.deleteVideo(videoId);

            return resUtil.sendSuccess({
                res,
                message: VideoMessages.success.DELETE_VIDEO_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[VideoController.deleteVideo]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },
};

export default VideoController;
