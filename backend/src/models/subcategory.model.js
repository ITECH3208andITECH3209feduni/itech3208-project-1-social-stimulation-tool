import mongoose from "mongoose";

const SubCategorySchema = new mongoose.Schema(
    {
        name: {
            type: String,
            required: true,
            trim: true,
        },
        categoryId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "Category",
            required: true,
        },
        description: {
            type: String,
            default: "",
            trim: true,
        },
    },
    {
        timestamps: true,
        createdAt: "createdAt",
        updatedAt: "updatedAt",
    },
);

const SubCategoryModel = mongoose.model("SubCategory", SubCategorySchema, "subcategories");
export default SubCategoryModel;
