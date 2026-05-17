"use client";

import { FormEvent, useState } from "react";

type Product = { id: number; name: string; price: string; stock: number };
type Category = { id: number; name: string };

export function AdminManager({ categories, products }: { categories: Category[]; products: Product[] }) {
  const [message, setMessage] = useState("");

  async function send(endpoint: string, body: Record<string, FormDataEntryValue | boolean | number | null>) {
    const response = await fetch(endpoint, {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(body)
    });
    const payload = await response.json();
    setMessage(response.ok ? "Guardado correctamente" : payload.error ?? "Error al guardar");
  }

  async function createProduct(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    await send("/api/products", {
      name: form.get("name") ?? "",
      description: form.get("description") ?? "",
      price: Number(form.get("price")),
      stock: Number(form.get("stock")),
      categoryId: Number(form.get("categoryId")),
      imageUrl: form.get("imageUrl") || null,
      featured: form.get("featured") === "on",
      isCustomizable: form.get("isCustomizable") === "on"
    });
  }

  async function createCategory(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    await send("/api/categories", {
      name: form.get("name") ?? "",
      description: form.get("description") ?? ""
    });
  }

  async function moveInventory(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = new FormData(event.currentTarget);
    await send("/api/inventory", {
      productId: Number(form.get("productId")),
      type: form.get("type") ?? "entrada",
      quantity: Number(form.get("quantity")),
      reason: form.get("reason") ?? ""
    });
  }

  return (
    <div className="grid">
      <form className="card card-body" onSubmit={createProduct}>
        <h2>Producto</h2>
        <label className="field"><span>Nombre</span><input name="name" required /></label>
        <label className="field"><span>Descripcion</span><textarea name="description" required /></label>
        <label className="field"><span>Precio</span><input name="price" type="number" step="0.01" required /></label>
        <label className="field"><span>Stock</span><input name="stock" type="number" required /></label>
        <label className="field"><span>Categoria</span><select name="categoryId">{categories.map((category) => <option key={category.id} value={category.id}>{category.name}</option>)}</select></label>
        <label className="field"><span>Imagen URL</span><input name="imageUrl" type="url" /></label>
        <label><input name="featured" type="checkbox" /> Destacado</label>
        <label><input name="isCustomizable" type="checkbox" /> Personalizable</label>
        <button className="button">Crear producto</button>
      </form>

      <form className="card card-body" onSubmit={createCategory}>
        <h2>Categoria</h2>
        <label className="field"><span>Nombre</span><input name="name" required /></label>
        <label className="field"><span>Descripcion</span><textarea name="description" /></label>
        <button className="button secondary">Crear categoria</button>
      </form>

      <form className="card card-body" onSubmit={moveInventory}>
        <h2>Inventario</h2>
        <label className="field"><span>Producto</span><select name="productId">{products.map((product) => <option key={product.id} value={product.id}>{product.name}</option>)}</select></label>
        <label className="field"><span>Tipo</span><select name="type"><option value="entrada">Entrada</option><option value="salida">Salida</option><option value="ajuste">Ajuste</option></select></label>
        <label className="field"><span>Cantidad</span><input name="quantity" type="number" required /></label>
        <label className="field"><span>Motivo</span><input name="reason" required /></label>
        <button className="button">Registrar movimiento</button>
      </form>
      {message ? <p className="notice">{message}</p> : null}
    </div>
  );
}
