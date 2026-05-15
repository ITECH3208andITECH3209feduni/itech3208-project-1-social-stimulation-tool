import { StatusCodes } from "http-status-codes";
import createError from "#config/error.config.js";

// MARK: - BUSINESS SUCCESS MESSAGES
const SUCCESS_MESSAGES = {
    GET_USER_INFOR_SUCCESSFULLY: "Get user information successfully.",
    UPLOAD_AVATAR_SUCCESSFULLY: "Your avatar upload successfully.",
    UPDATE_USER_PROFILE_SUCCESSFULLY: "Your profile updated successfully.",
};

// MARK: - BUSINESS ERROR MESSAGES
const ERROR_MESSAGES = {
    USERNAME_IS_EXIST: () =>
        createError({
            message: "This username is exist.",
            statusCode: StatusCodes.BAD_REQUEST,
            errorCode: "USERNAME_IS_EXIST",
        }),
    EMAIL_IS_EXIST: () =>
        createError({
            message: "This email is exist.",
            statusCode: StatusCodes.BAD_REQUEST,
            errorCode: "EMAIL_IS_EXIST",
        }),
    EMAIL_OR_USERNAME_IS_EXIST: () =>
        createError({
            message: "The email or username is exist.",
            statusCode: StatusCodes.BAD_REQUEST,
            errorCode: "EMAIL_OR_USERNAME_IS_EXIST",
        }),
    USERNAME_IS_NOT_CORRECT: () =>
        createError({
            message: "Your username is not correct. Please try again.",
            statusCode: StatusCodes.BAD_REQUEST,
            errorCode: "USERNAME_IS_NOT_CORRECT",
        }),
    PASSWORD_IS_NOT_CORRECT: () =>
        createError({
            message: "Your password is not correct. Please try again.",
            statusCode: StatusCodes.BAD_REQUEST,
            errorCode: "PASSWORD_IS_NOT_CORRECT",
        }),
    USER_IS_NOT_EXIST: () =>
        createError({
            message: "This user is not exist.",
            statusCode: StatusCodes.BAD_REQUEST,
            errorCode: "USER_IS_NOT_EXIST",
        }),
};

// MARK: - VALIDATION ERROR MESSAGES
const VALIDATION_MESSAGES = {
    username: {
        "string.base": "Username must be a string",
        "string.empty": "Username is required",
        "any.required": "Username is required",
        "string.min": "Username must be at least 5 characters",
        "string.max": "Username must be at most 20 characters",
    },
    email: {
        "string.base": "Email must be a string",
        "string.empty": "Email is required",
        "any.required": "Email is required",
        "string.email": "Email is not correct format",
    },
    password: {
        "string.base": "Password must be a string",
        "string.empty": "Password is required",
        "any.required": "Password is required",
        "string.min": "Password must be at least 5 characters",
        "string.max": "Password must be at most 20 characters",
    },
    confirmedPassword: {
        "string.base": "Password must be a string",
        "string.empty": "Password is required",
        "any.required": "Confirmed password is required",
        "any.only": "Confirm password must match password",
    },
    phone: {
        "string.base": "Phone must be a string",
        "string.empty": "Phone is required",
        "any.required": "Phone is required",
        "string.length": "Phone must be exactly 10 digits",
    },
    mimetype: {
        "any.required": "Avatar file is required",
        "string.empty": "Avatar file is required",
        "any.only": "Avatar must be png, jpg, jpeg or webp format",
    },

    size: {
        "any.required": "Avatar size is required",
        "number.max": "Avatar size must not exceed 5MB",
    },

    originalname: {
        "any.required": "Avatar filename is required",
        "string.empty": "Avatar filename is required",
    },
};

const UserMessages = {
    success: SUCCESS_MESSAGES,
    error: ERROR_MESSAGES,
    validation: VALIDATION_MESSAGES,
};

export default UserMessages;
