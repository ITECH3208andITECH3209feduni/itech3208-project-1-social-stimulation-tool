import mongoose from "mongoose";

const OrganizationSchema = new mongoose.Schema(
    {
        userId: {
            type: mongoose.Schema.Types.ObjectId,
            ref: "User",
            required: true,
            unique: true,
        },
        organizationName: {
            type: String,
            required: true,
            trim: true,
        },
        description: {
            type: String,
            default: "",
            trim: true,
        },
        website: {
            type: String,
            default: "",
            trim: true,
        },
        contactEmail: {
            type: String,
            default: "",
            trim: true,
        },
        contactPhone: {
            type: String,
            default: "",
            trim: true,
        },
        address: {
            type: String,
            default: "",
            trim: true,
        },
        logo: {
            url: {
                type: String,
                default: "",
            },
            cloudinaryId: {
                type: String,
                default: "",
            },
        },
        isVerified: {
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

const OrganizationModel = mongoose.model("Organization", OrganizationSchema, "organizations");
export default OrganizationModel;
