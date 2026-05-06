import { StatusCodes } from "http-status-codes";
import createError from "#config/error.config.js";

// MARK: - BUSINESS SUCCESS MESSAGES
const SUCCESS_MESSAGES = {
    INSERT_CATEGORY: "Insert new category successfully",
    BULK_INSERT_CATEGORY: "Initilize list categories successfully",
};

// MARK: - BUSINESS ERROR MESSAGES
const ERROR_MESSAGES = {
    INSERT_CATEGORY_FAILED: () =>
        createError({
            message: "Invalid category name",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
            errorCode: "INSERT_CATEGORY_FAILED",
        }),
    CATEGORY_NAME_EXIST: () =>
        createError({
            message: "Category name is already exist.",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
            errorCode: "CATEGORY_NAME_EXIST",
        }),
    LIST_CATEGORIES_EXIST: () =>
        createError({
            message: "List categories is already exist.",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
            errorCode: "LIST_CATEGORIES_EXIST",
        }),
};

// MARK: - VALIDATION ERROR MESSAGES
const VALIDATION_MESSAGES = {
    "string.base": "Name must be a string",
    "string.empty": "Name is required",
    "any.required": "Name is required",
    "string.min": "Name must be at least 3 characters",
    "string.max": "Name must be at most 50 characters",
};

const CategoryMessages = {
    success: SUCCESS_MESSAGES,
    error: ERROR_MESSAGES,
    validation: VALIDATION_MESSAGES,
};

export default CategoryMessages;
