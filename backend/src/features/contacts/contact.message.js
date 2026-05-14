import { StatusCodes } from "http-status-codes";
import createError from "#config/error.config.js";

// MARK: - BUSINESS SUCCESS MESSAGES
const SUCCESS_MESSAGES = {
    SEND_CONTACT_SUCCESSFULLY: "Your message has been sent successfully. We will get back to you soon.",
    GET_CONTACTS_SUCCESSFULLY: "Get contacts successfully.",
    UPDATE_CONTACT_STATUS_SUCCESSFULLY: "Contact status updated successfully.",
};

// MARK: - BUSINESS ERROR MESSAGES
const ERROR_MESSAGES = {
    CONTACT_NOT_FOUND: () =>
        createError({
            message: "This contact message does not exist.",
            statusCode: StatusCodes.NOT_FOUND,
            errorCode: "CONTACT_NOT_FOUND",
        }),
};

// MARK: - VALIDATION ERROR MESSAGES
const VALIDATION_MESSAGES = {
    message: {
        "string.base": "Message must be a string",
        "string.empty": "Message is required",
        "any.required": "Message is required",
        "string.max": "Message must be at most 2000 characters",
    },
    subject: {
        "string.base": "Subject must be a string",
        "string.max": "Subject must be at most 200 characters",
    },
    categoryId: {
        "string.base": "Category ID must be a string",
        "string.empty": "Category ID is required",
        "any.required": "Category ID is required",
        "string.hex": "Invalid Category ID format",
        "string.length": "Invalid Category ID format",
    },
};

const ContactMessages = {
    success: SUCCESS_MESSAGES,
    error: ERROR_MESSAGES,
    validation: VALIDATION_MESSAGES,
};

export default ContactMessages;
