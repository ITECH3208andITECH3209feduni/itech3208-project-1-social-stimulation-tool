import { StatusCodes } from "http-status-codes";
import { loggerUtil, resUtil } from "#utils/index.js";
import UserService from "./user.service.js";
import UserMessages from "./user.message.js";

const UserController = {
    getUserInfor: async (req, res) => {
        try {
            const userId = req.user.id;
            const userInfo = await UserService.getUserInfor(userId);
            resUtil.sendSuccess({
                res,
                message: UserMessages.success.GET_USER_INFOR_SUCCESSFULLY,
                data: userInfo,
            });
        } catch (error) {
            loggerUtil.error(`[UserController.getUserInfor]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    updateProfile: async (req, res) => {
        try {
            const userId = req.user.id;

            const updatedUser = await UserService.updateProfile({
                userId,
                payload: req.body,
            });

            return resUtil.sendSuccess({
                res,
                message: UserMessages.success.UPDATE_USER_PROFILE_SUCCESSFULLY,
                data: updatedUser,
            });
        } catch (error) {
            loggerUtil.error(`[UserController.updateProfile]: ${error}`);

            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    uploadAvatar: async (req, res) => {
        try {
            const userId = req.user.id;
            const file = req.file;

            const updatedUser = await UserService.uploadAvatar({
                userId,
                file,
            });

            return resUtil.sendSuccess({
                res,
                message: UserMessages.success.UPLOAD_AVATAR_SUCCESSFULLY,
                data: updatedUser,
            });
        } catch (error) {
            loggerUtil.error(`[UserController.uploadAvatar]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    getAllUsers: async (req, res) => {
        try {
            const { page, limit, role } = req.query;

            const result = await UserService.getAllUsers({ page, limit, role });

            return resUtil.sendSuccess({
                res,
                message: UserMessages.success.GET_USERS_SUCCESSFULLY,
                data: result,
            });
        } catch (error) {
            loggerUtil.error(`[UserController.getAllUsers]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    updateAccountStatus: async (req, res) => {
        try {
            const userId = req.params.id;
            const { accountStatus } = req.body;

            const updatedUser = await UserService.updateAccountStatus({
                userId,
                accountStatus,
            });

            return resUtil.sendSuccess({
                res,
                message: UserMessages.success.UPDATE_ACCOUNT_STATUS_SUCCESSFULLY,
                data: updatedUser,
            });
        } catch (error) {
            loggerUtil.error(`[UserController.updateAccountStatus]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },

    deleteUser: async (req, res) => {
        try {
            const userId = req.params.id;

            await UserService.deleteUser(userId);

            return resUtil.sendSuccess({
                res,
                message: UserMessages.success.DELETE_USER_SUCCESSFULLY,
            });
        } catch (error) {
            loggerUtil.error(`[UserController.deleteUser]: ${error}`);
            return resUtil.sendError({
                res,
                message: error.message,
                statusCode: error.statusCode || StatusCodes.INTERNAL_SERVER_ERROR,
                errorCode: error.errorCode,
            });
        }
    },
};

export default UserController;
