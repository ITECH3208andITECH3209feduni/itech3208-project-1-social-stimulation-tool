// MARK: - PLEASE DO NOT DELETE THIS FILE
import loggerUtil from "#utils/logger.utils.js";
import ipAccess from "./ip.access.js";

const runAccess = async () => {
    try {
        const ip = await ipAccess.getPublicIP();
        await ipAccess.addIP(ip);
        loggerUtil.debug(`Your current IP (${ip}) added to Atlas whitelist`);
    } catch (error) {
        loggerUtil.error(`[dev.runAccess]: ${error || error.response?.data || error.message}`);
        process.exit(1);
    }
};

export default runAccess;
