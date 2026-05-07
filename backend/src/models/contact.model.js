import mongoose from "mongoose";

const ContactSchema = new mongoose.Schema(
    {
        userId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "User",
            required: true,
        },
        categoryId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "Category",
            required: true,
        },
        subject: {
            type: String,
            trim: true,
            default: "General Inquiry",
        },
        message: {
            type: String,
            required: true,
            trim: true,
        },
        status: {
            type: String,
            enum: ["pending", "processed", "archived"],
            default: "pending",
        },
        acceptedTerms: {
            type: Boolean,
            default: false,
        },
    },
    {
        timestamps: true,
        createdAt: "createdAt",
        updatedAt: "updatedAt",
    },
);

const ContactModel = mongoose.model("Contact", ContactSchema, "contacts");
export default ContactModel;
