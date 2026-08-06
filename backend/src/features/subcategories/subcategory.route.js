import express from "express";
import { bodyMw, authMw } from "#middlewares/index.js";
import SubCategoryController from "./subcategory.controller.js";
import SubCategorySchema from "./subcategory.validation.js";

const publicRouter = express.Router();
const adminRouter = express.Router();

// MARK: - PUBLIC ROUTES
publicRouter.get("/", SubCategoryController.getSubCategories);
publicRouter.get("/:id", SubCategoryController.getSubCategoryById);

// MARK: - ADMIN ROUTES
adminRouter.post(
    "/",
    authMw.authorizeRole([authMw.UserRole.admin]),
    bodyMw.validate(SubCategorySchema.create),
    SubCategoryController.createSubCategory
);

adminRouter.put(
    "/:id",
    authMw.authorizeRole([authMw.UserRole.admin]),
    bodyMw.validate(SubCategorySchema.update),
    SubCategoryController.updateSubCategory
);

adminRouter.delete(
    "/:id",
    authMw.authorizeRole([authMw.UserRole.admin]),
    SubCategoryController.deleteSubCategory
);

export {
    publicRouter as SubCategoryPublicRouter,
    adminRouter as SubCategoryAdminRouter,
};
