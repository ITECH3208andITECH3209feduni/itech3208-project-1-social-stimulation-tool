import Joi from "joi";
import SubCategoryMessages from "./subcategory.message.js";

const createSubCategory = Joi.object({
    name: Joi.string().required().min(2).max(50).messages(SubCategoryMessages.validation.name),
    categoryId: Joi.string().required().messages(SubCategoryMessages.validation.categoryId),
    description: Joi.string().max(200).allow("").optional().messages(SubCategoryMessages.validation.description),
});

const updateSubCategory = Joi.object({
    name: Joi.string().min(2).max(50).optional().messages(SubCategoryMessages.validation.name),
    categoryId: Joi.string().optional().messages(SubCategoryMessages.validation.categoryId),
    description: Joi.string().max(200).allow("").optional().messages(SubCategoryMessages.validation.description),
});

const SubCategorySchema = {
    create: createSubCategory,
    update: updateSubCategory,
};

export default SubCategorySchema;
