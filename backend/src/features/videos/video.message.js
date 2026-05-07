import { StatusCodes } from "http-status-codes";
import createError from "#config/error.config.js";

// MARK: - BUSINESS SUCCESS MESSAGES
const SUCCESS_MESSAGES = {
    UPLOAD_VIDEO_SUCCESSFULLY: "Your video has been uploaded successfully.",
    UPLOAD_THUMBNAIL_SUCCESSFULLY: "Your video thumbnail has been uploaded successfully.",
    GET_VIDEO_SUCCESSFULLY: "Get video information successfully.",
    GET_VIDEOS_SUCCESSFULLY: "Get videos successfully.",
    DELETE_VIDEO_SUCCESSFULLY: "Video deleted successfully.",
    UPDATE_VIDEO_SUCCESSFULLY: "Video updated successfully.",
};

// MARK: - BUSINESS ERROR MESSAGES
const ERROR_MESSAGES = {
    VIDEO_NOT_FOUND: () =>
        createError({
            message: "This video does not exist.",
            statusCode: StatusCodes.NOT_FOUND,
            errorCode: "VIDEO_NOT_FOUND",
        }),
    VIDEO_FILE_REQUIRED: () =>
        createError({
            message: "Video file is required.",
            statusCode: StatusCodes.BAD_REQUEST,
            errorCode: "VIDEO_FILE_REQUIRED",
        }),
    CATEGORY_REQUIRED: () =>
        createError({
            message: "Category is required.",
            statusCode: StatusCodes.BAD_REQUEST,
            errorCode: "CATEGORY_REQUIRED",
        }),
    LEVEL_REQUIRED: () =>
        createError({
            message: "Level is required.",
            statusCode: StatusCodes.BAD_REQUEST,
            errorCode: "LEVEL_REQUIRED",
        }),
    UPLOAD_FAILED: () =>
        createError({
            message: "Failed to upload video. Please try again.",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
            errorCode: "UPLOAD_FAILED",
        }),
};

// MARK: - VALIDATION ERROR MESSAGES
const VALIDATION_MESSAGES = {
    title: {
        "string.base": "Title must be a string",
        "string.empty": "Title is required",
        "any.required": "Title is required",
        "string.min": "Title must be at least 3 characters",
        "string.max": "Title must be at most 200 characters",
    },
    description: {
        "string.base": "Description must be a string",
        "string.max": "Description must be at most 2000 characters",
    },
    categoryId: {
        "string.base": "Category ID must be a string",
        "string.empty": "Category ID is required",
        "any.required": "Category ID is required",
    },
    levelId: {
        "string.base": "Level ID must be a string",
        "string.empty": "Level ID is required",
        "any.required": "Level ID is required",
    },
    tags: {
        "array.base": "Tags must be an array",
    },
    // Video file validation
    mimetype: {
        "any.required": "Video file is required",
        "string.empty": "Video file is required",
        "any.only": "Video must be mp4, mkv, mov, avi or webm format",
    },
    size: {
        "any.required": "Video size is required",
        "number.max": "Video size must not exceed 500MB",
    },
    originalname: {
        "any.required": "Video filename is required",
        "string.empty": "Video filename is required",
    },
    // Thumbnail file validation
    thumbnailMimetype: {
        "any.required": "Thumbnail file is required",
        "string.empty": "Thumbnail file is required",
        "any.only": "Thumbnail must be png, jpg, jpeg or webp format",
    },
    thumbnailSize: {
        "any.required": "Thumbnail size is required",
        "number.max": "Thumbnail size must not exceed 5MB",
    },
};

const VideoMessages = {
    success: SUCCESS_MESSAGES,
    error: ERROR_MESSAGES,
    validation: VALIDATION_MESSAGES,
};

export default VideoMessages;
