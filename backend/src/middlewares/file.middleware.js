import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";

const validate = (schema) => (req, res, next) => {
    try {
        if (!req.file) {
            return resUtil.sendError({
                res,
                message: "File is required",
                statusCode: StatusCodes.BAD_REQUEST,
                errorCode: "FILE_REQUIRED",
            });
        }

        const data = req.file;

        const { error, value } = schema.validate(data, {
            abortEarly: false, // Get all error
            stripUnknown: true, // Remove invalid fields
        });

        // Parse errors
        if (error) {
            const formattedErrors = error.details.map((e) => ({
                path: e.path.join("."),
                message: e.message,
            }));

            return resUtil.sendError({
                res,
                message: "Validation failed",
                statusCode: StatusCodes.BAD_REQUEST,
                errorCode: "VALIDATION_ERROR",
                details: formattedErrors,
            });
        }

        next();
    } catch (error) {
        loggerUtil.error(`[fileMw.validate]: ${error}`);
        return resUtil.sendError({
            res,
            message: "Internal Server Error",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
        });
    }
};

const check = (req, res, next) => {
    if (!req.file && !req.headers["content-type"]?.includes("multipart")) {
        return resUtil.sendError({
            res,
            message: "Video file is required",
            statusCode: StatusCodes.BAD_REQUEST,
        });
    }
    next();
};

const fileMw = {
    validate,
    check,
};

export default fileMw;
