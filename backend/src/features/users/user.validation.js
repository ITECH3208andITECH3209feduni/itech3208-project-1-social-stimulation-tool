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

const UserSchema = { uploadAvatar };

export default UserSchema;
