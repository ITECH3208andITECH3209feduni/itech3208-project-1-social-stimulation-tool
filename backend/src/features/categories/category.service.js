import { CategoryModel } from "#models/index.js";
import CategoryMessages from "./category.message.js";

const CategoryService = {
    insertCategory: async (name) => {
        const exist = await CategoryModel.findOne({ name });

        if (exist) {
            throw CategoryMessages.error.CATEGORY_NAME_EXIST();
        }

        const category = await CategoryModel.create({ name });
        return category;
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

        return await CategoryModel.insertMany(categories);
    },

    getCategories: async () => {
        const categories = await CategoryModel.find().lean();

        if (!categories || categories.length == 0) {
            throw CategoryMessages.error.LIST_CATEGORIES_EMPTY();
        }

        const cleanedCategories = categories.map((c) => {
            return {
                id: c._id,
                name: c.name,
            };
        });

        return {
            total: categories.length,
            categories: cleanedCategories,
        };
    },
};

export default CategoryService;
