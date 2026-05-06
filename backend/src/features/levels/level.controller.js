import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";
import LevelService from "./level.service.js";
import LevelMessages from "./level.message.js";

const LevelController = {
    insertLevel: async (req, res) => {
        try {
            const level = req.body.level;
            const result = await LevelService.insertLevel(level);
            return resUtil.sendSuccess({
                res,
                message: LevelMessages.success.INSERT_CATEGORY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[LevelController.insertLevel]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    bulkInsertLevels: async (req, res) => {
        try {
            const result = await LevelService.bulkInsertLevels();
            return resUtil.sendSuccess({
                res,
                message: LevelMessages.success.BULK_INSERT_TUTORIAL_LEVELS,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[LevelController.bulkInsertLevels]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    getLevels: async (req, res) => {
        try {
            const result = await LevelService.getLevels();
            return resUtil.sendSuccess({
                res,
                message: LevelMessages.success.LIST_TUTORIAL_LEVELS_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[LevelController.getLevels]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },
};

export default LevelController;
