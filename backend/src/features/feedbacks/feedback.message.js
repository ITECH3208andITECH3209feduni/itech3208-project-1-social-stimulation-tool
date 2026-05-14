import { StatusCodes } from "http-status-codes";
import createError from "#config/error.config.js";

// MARK: - BUSINESS SUCCESS MESSAGES
const SUCCESS_MESSAGES = {
    CREATE_FEEDBACK_SUCCESSFULLY: "Your feedback has been posted successfully.",
    GET_FEEDBACKS_SUCCESSFULLY: "Get feedbacks successfully.",
    GET_TOP_FEEDBACKS_SUCCESSFULLY: "Get top feedbacks successfully.",
    DELETE_FEEDBACK_SUCCESSFULLY: "Feedback deleted successfully.",
    UPDATE_FEEDBACK_SUCCESSFULLY: "Feedback updated successfully.",
};

// MARK: - BUSINESS ERROR MESSAGES
const ERROR_MESSAGES = {
    FEEDBACK_NOT_FOUND: () =>
        createError({
            message: "This feedback does not exist.",
            statusCode: StatusCodes.NOT_FOUND,
            errorCode: "FEEDBACK_NOT_FOUND",
        }),
    PARENT_NOT_FOUND: () =>
        createError({
            message: "The original comment you are replying to does not exist.",
            statusCode: StatusCodes.NOT_FOUND,
            errorCode: "PARENT_NOT_FOUND",
        }),
};

// MARK: - VALIDATION ERROR MESSAGES
const VALIDATION_MESSAGES = {
    content: {
        "string.base": "Content must be a string",
        "string.empty": "Content cannot be empty",
        "any.required": "Content is required",
        "string.max": "Content must be at most 1000 characters",
    },
    videoId: {
        "string.base": "Video ID must be a string",
        "string.empty": "Video ID is required",
        "any.required": "Video ID is required",
        "string.hex": "Invalid Video ID format",
        "string.length": "Invalid Video ID format",
    },
    rating: {
        "number.base": "Rating must be a number",
        "number.min": "Rating must be at least 0",
        "number.max": "Rating must be at most 5",
    },
};

const FeedbackMessages = {
    success: SUCCESS_MESSAGES,
    error: ERROR_MESSAGES,
    validation: VALIDATION_MESSAGES,
};

export default FeedbackMessages;
