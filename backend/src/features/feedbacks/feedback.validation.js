import Joi from "joi";
import FeedbackMessages from "./feedback.message.js";

const createFeedback = Joi.object({
    content: Joi.string().max(1000).required().messages(FeedbackMessages.validation.content),

    rating: Joi.number().min(0).max(5).optional().messages(FeedbackMessages.validation.rating),
});

const updateFeedback = Joi.object({
    content: Joi.string().max(1000).optional().messages(FeedbackMessages.validation.content),

    rating: Joi.number().min(0).max(5).optional().messages(FeedbackMessages.validation.rating),
});

const getFeedbacks = Joi.object({
    page: Joi.number().integer().min(1).default(1).messages(FeedbackMessages.validation.page),
    limit: Joi.number().integer().max(100).default(9).messages(FeedbackMessages.validation.limit),
    isPinned: Joi.boolean().optional().messages(FeedbackMessages.validation.isPinned),
    rating: Joi.number()
        .integer()
        .min(0)
        .max(5)
        .optional()
        .messages(FeedbackMessages.validation.rating),
});

const FeedbackSchema = {
    createFeedback,
    updateFeedback,
    getFeedbacks,
};

export default FeedbackSchema;
