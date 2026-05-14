import { StatusCodes } from "http-status-codes";

class AppError extends Error {
    constructor({
        message,
        statusCode = StatusCodes.INTERNAL_SERVER_ERROR,
        errorCode = "INTERNAL_ERROR", // Business Error
        details = null, // Validation Error
    }) {
        super(message);

        this.name = "AppError";
        this.statusCode = statusCode;
        this.errorCode = errorCode;
        this.details = details;

        Error.captureStackTrace(this, this.constructor);
    }
}

const createError = ({ message, statusCode, errorCode, details = null }) => {
    return new AppError({ message, statusCode, errorCode, details });
};

export default createError;
