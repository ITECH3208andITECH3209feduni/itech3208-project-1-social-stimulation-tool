import { CategoryModel, SubCategoryModel } from "#models/index.js";
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
        const [categories, subCategories] = await Promise.all([
            CategoryModel.find().lean(),
            SubCategoryModel.find().sort({ name: 1 }).lean(),
        ]);

        if (!categories || categories.length === 0) {
            throw CategoryMessages.error.LIST_CATEGORIES_EMPTY();
        }

        const subCategoryMap = {};
        subCategories.forEach((subCat) => {
            const catIdStr = subCat.categoryId.toString();
            if (!subCategoryMap[catIdStr]) {
                subCategoryMap[catIdStr] = [];
            }
            const { _id, __v, categoryId, ...rest } = subCat;
            subCategoryMap[catIdStr].push({
                id: _id,
                ...rest,
            });
        });

        const formattedCategories = categories.map((category) => {
            const formatted = CategoryService._formatCategory(category);
            formatted.subCategories = subCategoryMap[formatted.id.toString()] || [];
            return formatted;
        });

        return {
            total: formattedCategories.length,
            categories: formattedCategories,
        };
    },

    deleteCategory: async (id) => {
        const existCategory = await CategoryModel.findById(id);

        if (!existCategory) {
            throw CategoryMessages.error.CATEGORY_IS_NOT_EXIST();
        }
        
        await CategoryModel.deleteOne({ _id: existCategory._id });
    },

    updateCategory: async (id, name) => {
        const category = await CategoryModel.findById(id);
        if (!category) {
            throw CategoryMessages.error.CATEGORY_IS_NOT_EXIST();
        }

        const duplicate = await CategoryModel.findOne({
            _id: { $ne: id },
            name,
        });

        if (duplicate) {
            throw CategoryMessages.error.CATEGORY_NAME_EXIST();
        }

        category.name = name;
        await category.save();

        return CategoryService._formatCategory(category);
    },
};

export default CategoryService;
