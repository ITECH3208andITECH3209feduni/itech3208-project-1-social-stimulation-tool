import { SubCategoryModel, CategoryModel } from "#models/index.js";
import SubCategoryMessages from "./subcategory.message.js";

const SubCategoryService = {
    _formatSubCategory: (subCategory) => {
        if (!subCategory) return null;
        const subCatObj = subCategory._doc || subCategory;
        const { _id, __v, categoryId, ...rest } = subCatObj;

        const formatted = {
            id: _id,
            ...rest,
        };

        if (categoryId) {
            formatted.category = categoryId._id
                ? {
                      id: categoryId._id,
                      name: categoryId.name,
                  }
                : categoryId;
        }

        return formatted;
    },

    createSubCategory: async ({ name, categoryId, description }) => {
        const categoryExist = await CategoryModel.findById(categoryId);
        if (!categoryExist) {
            throw SubCategoryMessages.error.CATEGORY_NOT_FOUND();
        }

        const subCategoryExist = await SubCategoryModel.findOne({ name, categoryId });
        if (subCategoryExist) {
            throw SubCategoryMessages.error.SUBCATEGORY_NAME_EXIST();
        }

        const newSubCategory = await SubCategoryModel.create({
            name,
            categoryId,
            description: description || "",
        });

        const populated = await newSubCategory.populate("categoryId", "name");
        return SubCategoryService._formatSubCategory(populated);
    },

    getSubCategories: async ({ categoryId } = {}) => {
        const filter = {};
        if (categoryId) {
            filter.categoryId = categoryId;
        }

        const subCategories = await SubCategoryModel.find(filter)
            .populate("categoryId", "name")
            .sort({ name: 1 })
            .lean();

        return {
            total: subCategories.length,
            subCategories: subCategories.map(SubCategoryService._formatSubCategory),
        };
    },

    getSubCategoryById: async (id) => {
        const subCategory = await SubCategoryModel.findById(id)
            .populate("categoryId", "name")
            .lean();

        if (!subCategory) {
            throw SubCategoryMessages.error.SUBCATEGORY_NOT_FOUND();
        }

        return SubCategoryService._formatSubCategory(subCategory);
    },

    updateSubCategory: async (id, payload) => {
        const subCategory = await SubCategoryModel.findById(id);
        if (!subCategory) {
            throw SubCategoryMessages.error.SUBCATEGORY_NOT_FOUND();
        }

        if (payload.categoryId) {
            const categoryExist = await CategoryModel.findById(payload.categoryId);
            if (!categoryExist) {
                throw SubCategoryMessages.error.CATEGORY_NOT_FOUND();
            }
            subCategory.categoryId = payload.categoryId;
        }

        if (payload.name) {
            const targetCategoryId = payload.categoryId || subCategory.categoryId;
            const duplicateExist = await SubCategoryModel.findOne({
                _id: { $ne: id },
                name: payload.name,
                categoryId: targetCategoryId,
            });
            if (duplicateExist) {
                throw SubCategoryMessages.error.SUBCATEGORY_NAME_EXIST();
            }
            subCategory.name = payload.name;
        }

        if (payload.description !== undefined) {
            subCategory.description = payload.description;
        }

        await subCategory.save();
        const populated = await subCategory.populate("categoryId", "name");
        return SubCategoryService._formatSubCategory(populated);
    },

    deleteSubCategory: async (id) => {
        const subCategory = await SubCategoryModel.findById(id);
        if (!subCategory) {
            throw SubCategoryMessages.error.SUBCATEGORY_NOT_FOUND();
        }

        await SubCategoryModel.deleteOne({ _id: id });
        return { id };
    },
};

export default SubCategoryService;
