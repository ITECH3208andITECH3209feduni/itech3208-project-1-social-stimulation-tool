import { StatusCodes } from "http-status-codes";
import createError from "#config/error.config.js";

// MARK: - BUSINESS SUCCESS MESSAGES
const SUCCESS_MESSAGES = {
    INSERT_LEVEL: "Insert new level successfully",
    BULK_INSERT_TUTORIAL_LEVELS: "Initilize list levels successfully",
    LIST_TUTORIAL_LEVELS_SUCCESSFULLY: "Get list levels successfully",
    DELETE_LEVEL_SUCCESSFULLY: "Delete level successfully",
};

// MARK: - BUSINESS ERROR MESSAGES
const ERROR_MESSAGES = {
    INSERT_TUTORIAL_LEVEL_FAILED: () =>
        createError({
            message: "Invalid tutorial level name",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
            errorCode: "INSERT_TUTORIAL_LEVEL_FAILED",
        }),
    TUTORIAL_LEVEL_IS_EXIST: () =>
        createError({
            message: "Tutorial level is already exist.",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
            errorCode: "TUTORIAL_LEVEL_IS_EXIST",
        }),
    LIST_TUTORIAL_LEVELS_EXIST: () =>
        createError({
            message: "List tutorial levels is already exist.",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
            errorCode: "LIST_TUTORIAL_LEVELS_EXIST",
        }),
    LIST_TUTORIAL_LEVELS_IS_EMPTY: () =>
        createError({
            message: "List tutorial levels is empty.",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
            errorCode: "LIST_TUTORIAL_LEVELS_IS_EMPTY",
        }),
    TUTORIAL_LEVEL_IS_NOT_EXIST: () =>
        createError({
            message: "Tutorial levels is not exist.",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
            errorCode: "TUTORIAL_LEVEL_IS_NOT_EXIST",
        }),
};

// MARK: - VALIDATION ERROR MESSAGES
const VALIDATION_MESSAGES = {
    "string.base": "Level must be a string",
    "string.empty": "Level is required",
    "any.required": "Level is required",
    "string.min": "Level must be at least 3 characters",
    "string.max": "Level must be at most 50 characters",
};

const LevelMessages = {
    success: SUCCESS_MESSAGES,
    error: ERROR_MESSAGES,
    validation: VALIDATION_MESSAGES,
};

export default LevelMessages;
