import mongoose from "mongoose";
import { envConfig } from "./index.js";
import { loggerUtil } from "#utils/index.js";

// Keep a small warm pool so the first request does not need to establish a new
// connection to Atlas. Timeouts prevent requests from waiting indefinitely when
// the database is unavailable.
const mongoOptions = {
    maxPoolSize: 10,
    minPoolSize: 1,
    maxConnecting: 2,
    maxIdleTimeMS: 30_000,
    waitQueueTimeoutMS: 10_000,
    serverSelectionTimeoutMS: 10_000,
    connectTimeoutMS: 10_000,
    socketTimeoutMS: 45_000,
    heartbeatFrequencyMS: 10_000,
    retryReads: true,
    retryWrites: true,
};

const mongoConfig = {
    connect: async () => {
        if (!envConfig.mongoUrl) {
            throw new Error("MONGODB_URL is not configured");
        }

        try {
            await mongoose.connect(envConfig.mongoUrl, mongoOptions);
            loggerUtil.debug("Connect to database successfully");
        } catch (error) {
            loggerUtil.error(`[mongoConfig.connect]: ${error}`);
            // Do not let the API accept requests without a database connection.
            throw error;
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
