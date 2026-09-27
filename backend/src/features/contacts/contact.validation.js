import Joi from "joi";
import ContactMessages from "./contact.message.js";

const sendContact = Joi.object({
    name: Joi.string()
        .trim()
        .required()
        .messages({
            "string.base": "Name must be a string",
            "string.empty": "Name is required",
            "any.required": "Name is required",
        }),

    email: Joi.string()
        .email()
        .trim()
        .required()
        .messages({
            "string.base": "Email must be a string",
            "string.empty": "Email is required",
            "string.email": "Please enter a valid email address",
            "any.required": "Email is required",
        }),

    message: Joi.string()
        .trim()
        .max(2000)
        .required()
        .messages(ContactMessages.validation.message),
});

const updateStatus = Joi.object({
    status: Joi.string()
        .valid("pending", "in progress", "resolved", "rejected")
        .required(),
});

const ContactSchema = {
    sendContact,
    updateStatus,
};

export default ContactSchema;
