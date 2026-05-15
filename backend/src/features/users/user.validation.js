import Joi from "joi";
import UserMessages from "./user.message.js";

const uploadAvatar = Joi.object({
    mimetype: Joi.string()
        .valid("image/png", "image/jpeg", "image/jpg", "image/webp")
        .required()
        .messages(UserMessages.validation.mimetype),

    size: Joi.number()
        .max(5 * 1024 * 1024)
        .required()
        .messages(UserMessages.validation.size),

    originalname: Joi.string().required().messages(UserMessages.validation.originalname),
});

const updateProfile = Joi.object({
    firstName: Joi.string().allow("").optional(),

    lastName: Joi.string().allow("").optional(),

    username: Joi.string()
        .min(5)
        .max(20)
        .optional()
        .messages(UserMessages.validation.username),

    email: Joi.string()
        .email()
        .optional()
        .messages(UserMessages.validation.email),
});

const UserSchema = {
    uploadAvatar,
    updateProfile,
};

export default UserSchema;
