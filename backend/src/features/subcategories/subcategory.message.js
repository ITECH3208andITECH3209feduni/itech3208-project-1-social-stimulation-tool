import { StatusCodes } from "http-status-codes";
import createError from "#config/error.config.js";

// MARK: - BUSINESS SUCCESS MESSAGES
const SUCCESS_MESSAGES = {
    CREATE_SUBCATEGORY_SUCCESSFULLY: "Create sub-category successfully.",
    GET_SUBCATEGORIES_SUCCESSFULLY: "Get list of sub-categories successfully.",
    GET_SUBCATEGORY_SUCCESSFULLY: "Get sub-category successfully.",
    UPDATE_SUBCATEGORY_SUCCESSFULLY: "Update sub-category successfully.",
    DELETE_SUBCATEGORY_SUCCESSFULLY: "Delete sub-category successfully.",
};

// MARK: - BUSINESS ERROR MESSAGES
const ERROR_MESSAGES = {
    SUBCATEGORY_NAME_EXIST: () =>
        createError({
            message: "Sub-category name already exists for this category.",
            statusCode: StatusCodes.BAD_REQUEST,
            errorCode: "SUBCATEGORY_NAME_EXIST",
        }),
    SUBCATEGORY_NOT_FOUND: () =>
        createError({
            message: "Sub-category does not exist.",
            statusCode: StatusCodes.NOT_FOUND,
            errorCode: "SUBCATEGORY_NOT_FOUND",
        }),
    CATEGORY_NOT_FOUND: () =>
        createError({
            message: "Parent category does not exist.",
            statusCode: StatusCodes.NOT_FOUND,
            errorCode: "CATEGORY_NOT_FOUND",
        }),
};

// MARK: - VALIDATION ERROR MESSAGES
const VALIDATION_MESSAGES = {
    name: {
        "string.base": "Name must be a string",
        "string.empty": "Name is required",
        "any.required": "Name is required",
        "string.min": "Name must be at least 2 characters",
        "string.max": "Name must be at most 50 characters",
    },
    categoryId: {
        "string.base": "Category ID must be a string",
        "string.empty": "Category ID is required",
        "any.required": "Category ID is required",
    },
    description: {
        "string.base": "Description must be a string",
        "string.max": "Description must be at most 200 characters",
    },
};

const SubCategoryMessages = {
    success: SUCCESS_MESSAGES,
    error: ERROR_MESSAGES,
    validation: VALIDATION_MESSAGES,
};

export default SubCategoryMessages;
