import { CategoryModel } from "#models/index.js";
import CategoryMessages from "./category.message.js";

const CategoryService = {
    _formatCategory: (category) => {
        if (!category) return null;
        const { _id, __v, isDeleted, ...rest } = category._doc || category;
        return {
            id: _id,
            ...rest,
        };
    },

    insertCategory: async (name) => {
        const exist = await CategoryModel.findOne({ name });

        if (exist) {
            throw CategoryMessages.error.CATEGORY_NAME_EXIST();
        }

        const category = await CategoryModel.create({ name });
        return CategoryService._formatCategory(category);
    },

    bulkInsertCategories: async () => {
        const count = await CategoryModel.countDocuments();
        const isEmpty = count == 0;

        if (!isEmpty) {
            throw CategoryMessages.error.LIST_CATEGORIES_EXIST();
        }

        const categories = [
            { name: "All" },
            { name: "Social" },
            { name: "Services" },
            { name: "Entertainment" },
            { name: "Job Interviews" },
            { name: "Medical" },
            { name: "Food" },
            { name: "Accommodation" },
        ];

        const inserted = await CategoryModel.insertMany(categories);
        return inserted.map(CategoryService._formatCategory);
    },

    getCategories: async () => {
        const categories = await CategoryModel.find().lean();

        if (!categories || categories.length == 0) {
            throw CategoryMessages.error.LIST_CATEGORIES_EMPTY();
        }

        return {
            total: categories.length,
            categories: categories.map(CategoryService._formatCategory),
        };
    },

    deleteCategory: async (id) => {
        const existCategory = await CategoryModel.findById(id);

        if (!existCategory) {
            throw CategoryMessages.error.CATEGORY_IS_NOT_EXIST();
        }
        
        await CategoryModel.deleteOne({ _id: existCategory._id });
    },
};

export default CategoryService;
