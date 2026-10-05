import mongoose from "mongoose";

const ContactSchema = new mongoose.Schema(
    {
        userId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "User",
            required: true,
        },
        name: {
            type: String,
            required: true,
            trim: true,
        },
        email: {
            type: String,
            required: true,
            trim: true,
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
            enum: ["pending", "in progress", "resolved", "rejected"],
            default: "pending",
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