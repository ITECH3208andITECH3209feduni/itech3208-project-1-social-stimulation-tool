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
        message: {
            type: String,
        },
        acceptedTerms: {
            type: Boolean,
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
