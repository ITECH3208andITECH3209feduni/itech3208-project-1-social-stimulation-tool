import mongoose from "mongoose";
import { envConfig } from "./index.js";
import { loggerUtil } from "#utils/index.js";

const mongoOptions = {};

const mongoConfig = {
    connect: async () => {
        try {
            await mongoose.connect(envConfig.mongoUrl, mongoOptions);
            loggerUtil.debug("Connect to database successfully");
        } catch (error) {
            loggerUtil.error(`[mongoConfig.connect]: ${error}`);
        }
    },

    disconnect: async () => {
        try {
            await mongoose.disconnect();
            loggerUtil.debug(`Disconnect to DB successfully`);
        } catch (error) {
            loggerUtil.error(`[mongoConfig.disconnect]: ${error}`);
        }
    },
};

export default mongoConfig;
