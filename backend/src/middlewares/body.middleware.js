import Joi from "joi";
import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";

const validate = (schema) => (req, res, next) => {
    try {
        const { error, value } = schema.validate(req.body, {
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

        // Overwrite body with cleaned data
        req.body = value;

        next();
    } catch (error) {
        loggerUtil.error(`[bodyMw.validate]: ${error}`);
        return resUtil.sendError({
            res,
            message: "Internal Server Error",
            statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
        });
    }
};

const bodyMw = {
    validate,
};

export default bodyMw;
