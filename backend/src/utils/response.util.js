import { StatusCodes, ReasonPhrases } from "http-status-codes";
/**
 * Send Response when request success
 * @param {Object} res - Object response from express
 * @param {string} message - Message to response
 * @param {Object} data - Data to return, in default data is {}
 * @param {number} statusCode - http code, in default, the code is 200
 */
const sendSuccess = ({ res, message, data = {}, statusCode = StatusCodes.OK }) => {
    return res.status(statusCode).json({
        success: true,
        message,
        data,
    });
};

/**
 * Send Response when request error
 * @param {Object} res - Object response from express
 * @param {string} message - Message when response failure
 * @param {number} statusCode - http code, in default, the code is 400
 * @param {number} errorCode - business code, in default, the code is null
 */
const sendError = ({ res, message, statusCode = StatusCodes.BAD_REQUEST, errorCode = null }) => {
    return res.status(statusCode).json({
        success: false,
        statusCode, // HTTP error
        message,
        errorCode, // Business logic error
        error: ReasonPhrases[statusCode] || "Error",
    });
};

const resUtil = { sendSuccess, sendError };

export default resUtil;
