import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";
import SubCategoryService from "./subcategory.service.js";
import SubCategoryMessages from "./subcategory.message.js";

const SubCategoryController = {
    createSubCategory: async (req, res) => {
        try {
            const { name, categoryId, description } = req.body;
            const newSubCategory = await SubCategoryService.createSubCategory({
                name,
                categoryId,
                description,
            });

            return resUtil.sendSuccess({
                res,
                statusCode: StatusCodes.CREATED,
                message: SubCategoryMessages.success.CREATE_SUBCATEGORY_SUCCESSFULLY,
                data: newSubCategory,
            });
        } catch (error) {
            loggerUtil.error(`[SubCategoryController.createSubCategory]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    getSubCategories: async (req, res) => {
        try {
            const { categoryId } = req.query;
            const result = await SubCategoryService.getSubCategories({ categoryId });

            return resUtil.sendSuccess({
                res,
                message: SubCategoryMessages.success.GET_SUBCATEGORIES_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[SubCategoryController.getSubCategories]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    getSubCategoryById: async (req, res) => {
        try {
            const id = req.params.id;
            const subCategory = await SubCategoryService.getSubCategoryById(id);

            return resUtil.sendSuccess({
                res,
                message: SubCategoryMessages.success.GET_SUBCATEGORY_SUCCESSFULLY,
                data: subCategory,
            });
        } catch (error) {
            loggerUtil.error(`[SubCategoryController.getSubCategoryById]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    updateSubCategory: async (req, res) => {
        try {
            const id = req.params.id;
            const payload = req.body;
            const updated = await SubCategoryService.updateSubCategory(id, payload);

            return resUtil.sendSuccess({
                res,
                message: SubCategoryMessages.success.UPDATE_SUBCATEGORY_SUCCESSFULLY,
                data: updated,
            });
        } catch (error) {
            loggerUtil.error(`[SubCategoryController.updateSubCategory]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    deleteSubCategory: async (req, res) => {
        try {
            const id = req.params.id;
            const result = await SubCategoryService.deleteSubCategory(id);

            return resUtil.sendSuccess({
                res,
                message: SubCategoryMessages.success.DELETE_SUBCATEGORY_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[SubCategoryController.deleteSubCategory]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },
};

export default SubCategoryController;
