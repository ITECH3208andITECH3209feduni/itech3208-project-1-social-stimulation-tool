import Joi from "joi";
import CategoryMessages from "./category.message.js";

const categorySchema = Joi.object({
    name: Joi.string().required().min(3).max(50).messages(CategoryMessages.validation),
});

export default categorySchema;
