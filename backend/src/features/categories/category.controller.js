import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";
import CategoryService from "./category.service.js";
import CategoryMessages from "./category.message.js";

const CategoryController = {
    insertCategory: async (req, res) => {
        try {
            const categoryName = req.body.name;
            const result = await CategoryService.insertCategory(categoryName);
            return resUtil.sendSuccess({
                res,
                message: CategoryMessages.success.INSERT_CATEGORY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[CategoryController.insertCategory]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    bulkInsertCategories: async (req, res) => {
        try {
            const result = await CategoryService.bulkInsertCategories();
            return resUtil.sendSuccess({
                res,
                message: CategoryMessages.success.BULK_INSERT_CATEGORY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[CategoryController.bulkInsertCategories]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    getCategories: async (req, res) => {
        try {
            const result = await CategoryService.getCategories();
            return resUtil.sendSuccess({
                res,
                message: CategoryMessages.success.LIST_CATEGORIES_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[CategoryController.getCategories]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    deleteCategory: async (req, res) => {
        try {
            const id = req.params.id;
            await CategoryService.deleteCategory(id);
            return resUtil.sendSuccess({
                res,
                message: CategoryMessages.success.DELETE_CATEGORY_SUCCESSFULLY,
            });
        } catch (error) {
            loggerUtil.error(`[CategoryController.deleteCategory]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },
};

export default CategoryController;
