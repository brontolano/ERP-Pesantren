import { api } from './client'

export interface WilayahItem {
  code: string
  name: string
  postal_code?: string
}

const DEMO = import.meta.env.VITE_DEMO_MODE === 'true'

export const wilayahApi = {
  provinces: (): Promise<WilayahItem[]> =>
    DEMO
      ? Promise.resolve([
          { code: '32', name: 'Jawa Barat' },
          { code: '33', name: 'Jawa Tengah' },
          { code: '34', name: 'DI Yogyakarta' },
          { code: '35', name: 'Jawa Timur' },
        ])
      : api.get<{ data: WilayahItem[] }>('/wilayah/provinces').then(r => r.data.data),

  cities: (provinceCode: string): Promise<WilayahItem[]> =>
    DEMO
      ? Promise.resolve([
          { code: '32.07', name: 'Kabupaten Sumedang' },
          { code: '32.13', name: 'Kota Bandung' },
        ])
      : api.get<{ data: WilayahItem[] }>(`/wilayah/cities/${provinceCode}`).then(r => r.data.data),

  districts: (cityCode: string): Promise<WilayahItem[]> =>
    DEMO
      ? Promise.resolve([
          { code: '32.07.19', name: 'Cimalaka' },
          { code: '32.07.01', name: 'Sumedang Selatan' },
        ])
      : api.get<{ data: WilayahItem[] }>(`/wilayah/districts/${cityCode}`).then(r => r.data.data),

  villages: (districtCode: string): Promise<WilayahItem[]> =>
    DEMO
      ? Promise.resolve([
          { code: '32.07.19.2001', name: 'Cimalaka', postal_code: '45353' },
          { code: '32.07.19.2002', name: 'Mandalaherang', postal_code: '45353' },
        ])
      : api.get<{ data: WilayahItem[] }>(`/wilayah/villages/${districtCode}`).then(r => r.data.data),

  search: (query: string): Promise<WilayahItem[]> =>
    DEMO
      ? Promise.resolve([])
      : api.get<{ data: WilayahItem[] }>(`/wilayah/search/${encodeURIComponent(query)}`).then(r => r.data.data),
}
