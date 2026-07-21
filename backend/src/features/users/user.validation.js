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
    firstName: Joi.string().messages(UserMessages.validation.firstName),

    lastName: Joi.string().messages(UserMessages.validation.lastName),

    email: Joi.string().email().messages(UserMessages.validation.email),

    location: Joi.string().messages(UserMessages.validation.location),
});

const updateAccountStatus = Joi.object({
    accountStatus: Joi.string()
        .valid("active", "inactive", "suspended", "banned")
        .required()
        .messages(UserMessages.validation.accountStatus),
});

const UserSchema = {
    uploadAvatar,
    updateProfile,
    updateAccountStatus,
};

export default UserSchema;
