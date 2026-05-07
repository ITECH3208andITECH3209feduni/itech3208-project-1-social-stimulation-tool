import Joi from "joi";
import AuthMessages from "./auth.message.js";

const registerUser = Joi.object({
    username: Joi.string().min(5).max(20).required().messages(AuthMessages.validation.username),
    email: Joi.string().email().required().messages(AuthMessages.validation.email),
    password: Joi.string().min(5).max(20).required().messages(AuthMessages.validation.password),
    confirmedPassword: Joi.string()
        .valid(Joi.ref("password"))
        // .strip()
        .required()
        .messages(AuthMessages.validation.confirmedPassword),
    phone: Joi.string().required().length(10).messages(AuthMessages.validation.phone),
    role: Joi.string()
        .valid("individual", "organization")
        .default("individual")
        .optional()
        .messages(AuthMessages.validation.role),
});

const loginUser = Joi.object({
    username: Joi.string().min(5).max(20).required().messages(AuthMessages.validation.username),
    password: Joi.string().min(5).max(20).required().messages(AuthMessages.validation.password),
});

const AuthSchema = {
    registerUser,
    loginUser
};

export default AuthSchema;
