import { AxiosInstance } from 'axios';
import { BullhornAllSettingsAndEntitlementsResponse } from '../types';
/**
 * A Class that defines a service to grab settings from Bullhorn
 */
export declare class SettingService {
    http: AxiosInstance;
    constructor();
    getSettings(settings: string[]): Promise<{
        [key: string]: any;
    }>;
    getEntitlements(entity: string): Promise<string[]>;
    getAllSettingsAndEntitlements(): Promise<BullhornAllSettingsAndEntitlementsResponse>;
}
