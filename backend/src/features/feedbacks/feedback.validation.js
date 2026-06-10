import Joi from "joi";
import FeedbackMessages from "./feedback.message.js";

const createFeedback = Joi.object({
    content: Joi.string()
        .max(1000)
        .required()
        .messages(FeedbackMessages.validation.content),
    
    rating: Joi.number()
        .min(0)
        .max(5)
        .optional()
        .messages(FeedbackMessages.validation.rating),
});

const updateFeedback = Joi.object({
    content: Joi.string()
        .max(1000)
        .optional()
        .messages(FeedbackMessages.validation.content),
    
    rating: Joi.number()
        .min(0)
        .max(5)
        .optional()
        .messages(FeedbackMessages.validation.rating),
});

const FeedbackSchema = {
    createFeedback,
    updateFeedback,
};

export default FeedbackSchema;
