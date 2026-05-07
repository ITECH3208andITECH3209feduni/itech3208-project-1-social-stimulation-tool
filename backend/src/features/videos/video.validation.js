import Joi from "joi";
import VideoMessages from "./video.message.js";

// Validation schema for uploading a video file (multipart)
const uploadVideo = Joi.object({
    mimetype: Joi.string()
        .valid("video/mp4", "video/x-matroska", "video/quicktime", "video/x-msvideo", "video/webm")
        .required()
        .messages(VideoMessages.validation.mimetype),

    size: Joi.number()
        .max(500 * 1024 * 1024) // 500MB
        .required()
        .messages(VideoMessages.validation.size),

    originalname: Joi.string().required().messages(VideoMessages.validation.originalname),
});

// Validation schema for uploading a thumbnail image
const uploadThumbnail = Joi.object({
    mimetype: Joi.string()
        .valid("image/png", "image/jpeg", "image/jpg", "image/webp")
        .required()
        .messages(VideoMessages.validation.thumbnailMimetype),

    size: Joi.number()
        .max(5 * 1024 * 1024) // 5MB
        .required()
        .messages(VideoMessages.validation.thumbnailSize),

    originalname: Joi.string().required().messages(VideoMessages.validation.originalname),
});

// Validation schema for video metadata (sent as text fields inside multipart/form-data)
const createVideo = Joi.object({
    title: Joi.string().min(3).max(200).required().messages(VideoMessages.validation.title),

    description: Joi.string()
        .max(2000)
        .allow("")
        .optional()
        .messages(VideoMessages.validation.description),

    categoryId: Joi.string().required().messages(VideoMessages.validation.categoryId),

    levelId: Joi.string().required().messages(VideoMessages.validation.levelId),

    // multipart sends array fields as string (single value) or array (multiple values)
    tags: Joi.alternatives()
        .try(Joi.array().items(Joi.string()), Joi.string())
        .optional()
        .messages(VideoMessages.validation.tags),
});

const VideoSchema = { uploadVideo, uploadThumbnail, createVideo };

export default VideoSchema;
