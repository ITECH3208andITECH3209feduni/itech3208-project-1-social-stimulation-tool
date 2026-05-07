import Joi from "joi";
import ContactMessages from "./contact.message.js";

const sendContact = Joi.object({
    categoryId: Joi.string()
        .hex()
        .length(24)
        .required()
        .messages(ContactMessages.validation.categoryId),
    
    subject: Joi.string()
        .max(200)
        .optional()
        .allow("", null)
        .messages(ContactMessages.validation.subject),
    
    message: Joi.string()
        .max(2000)
        .required()
        .messages(ContactMessages.validation.message),
    
    acceptedTerms: Joi.boolean()
        .optional()
        .default(true),
});

const updateStatus = Joi.object({
    status: Joi.string()
        .valid("pending", "processed", "archived")
        .required(),
});

const ContactSchema = {
    sendContact,
    updateStatus,
};

export default ContactSchema;
