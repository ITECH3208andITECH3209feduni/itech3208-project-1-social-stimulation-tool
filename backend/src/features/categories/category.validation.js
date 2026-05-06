import Joi from "joi";
import CategoryMessages from "./category.message.js";

const insertCategory = Joi.object({
    name: Joi.string().required().min(3).max(50).messages(CategoryMessages.validation),
});

const CategorySchema = {
    insert: insertCategory,
};

export default CategorySchema;
